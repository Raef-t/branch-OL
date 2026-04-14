<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

class ConsolidateMigrations extends Command
{
    protected $signature = 'migrations:consolidate
        {--write : Generate consolidated migration files}
        {--archive : Move old migration files to database/migrations_archive}
        {--output=database/migrations_consolidated : Output directory for generated files}
        {--table=* : Limit consolidation to specific table names}';

    protected $description = 'Consolidate scattered migrations into one migration file per table, ordered by time.';

    public function handle(): int
    {
        try {
            $migrationFiles = $this->collectMigrationFiles();
            $tableFilter = $this->normalizeTableFilter((array) $this->option('table'));

            $grouped = $this->groupStatementsByTable($migrationFiles, $tableFilter);

            if (empty($grouped)) {
                $this->warn('No table statements were found for consolidation.');
                return self::SUCCESS;
            }

            $this->printSummary($grouped);

            if (!$this->option('write')) {
                $this->line('');
                $this->info('Dry run complete. Re-run with --write to generate files.');
                return self::SUCCESS;
            }

            $outputDirectory = base_path((string) $this->option('output'));
            $this->writeConsolidatedFiles($grouped, $outputDirectory);

            if ($this->option('archive')) {
                $this->archiveOriginalMigrations($migrationFiles);
            }

            $this->line('');
            $this->info('Migration consolidation completed successfully.');
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * @return array<int, array{path:string,name:string,timestamp:string,relative:string}>
     */
    protected function collectMigrationFiles(): array
    {
        $roots = [
            base_path('database/migrations'),
            base_path('Modules'),
        ];

        $files = [];

        foreach ($roots as $root) {
            if (!File::exists($root)) {
                continue;
            }

            if ($root === base_path('database/migrations')) {
                foreach ((array) File::files($root) as $file) {
                    if ($file->getExtension() !== 'php') {
                        continue;
                    }

                    $name = $file->getFilename();
                    if (!str_contains($name, '_')) {
                        continue;
                    }

                    $files[] = $this->buildMigrationMeta($file->getPathname(), $name);
                }

                continue;
            }

            $moduleMigrationDirs = File::glob($root . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations');

            foreach ($moduleMigrationDirs as $migrationDir) {
                if (!File::isDirectory($migrationDir)) {
                    continue;
                }

                foreach ((array) File::files($migrationDir) as $file) {
                    if ($file->getExtension() !== 'php') {
                        continue;
                    }

                    $name = $file->getFilename();
                    if (!str_contains($name, '_')) {
                        continue;
                    }

                    $files[] = $this->buildMigrationMeta($file->getPathname(), $name);
                }
            }
        }

        usort(
            $files,
            fn (array $a, array $b): int => $a['timestamp'] <=> $b['timestamp'] ?: $a['name'] <=> $b['name']
        );

        return $files;
    }

    /**
     * @param array<int, string> $raw
     * @return array<int, string>
     */
    protected function normalizeTableFilter(array $raw): array
    {
        $normalized = [];

        foreach ($raw as $entry) {
            foreach (explode(',', $entry) as $item) {
                $table = trim($item);
                if ($table === '') {
                    continue;
                }

                $normalized[] = strtolower($table);
            }
        }

        return array_values(array_unique($normalized));
    }

    /**
     * @param array<int, array{path:string,name:string,timestamp:string,relative:string}> $migrationFiles
     * @param array<int, string> $tableFilter
     * @return array<string, array{first_timestamp:string,statements:array<int, array{statement:string,source:string,timestamp:string}>}>
     */
    protected function groupStatementsByTable(array $migrationFiles, array $tableFilter): array
    {
        $grouped = [];

        foreach ($migrationFiles as $migration) {
            $contents = File::get($migration['path']);
            $upBody = $this->extractMethodBody($contents, 'up');

            if ($upBody === null || trim($upBody) === '') {
                continue;
            }

            $statements = $this->splitTopLevelStatements($upBody);

            foreach ($statements as $statement) {
                $tables = $this->extractTablesFromStatement($statement);
                if (empty($tables)) {
                    continue;
                }

                foreach ($tables as $table) {
                    if (!empty($tableFilter) && !in_array(strtolower($table), $tableFilter, true)) {
                        continue;
                    }

                    if (!isset($grouped[$table])) {
                        $grouped[$table] = [
                            'first_timestamp' => $migration['timestamp'],
                            'statements' => [],
                        ];
                    }

                    $grouped[$table]['statements'][] = [
                        'statement' => trim($statement),
                        'source' => $migration['relative'],
                        'timestamp' => $migration['timestamp'],
                    ];
                }
            }
        }

        foreach ($grouped as $table => $payload) {
            usort(
                $grouped[$table]['statements'],
                fn (array $a, array $b): int => $a['timestamp'] <=> $b['timestamp'] ?: $a['source'] <=> $b['source']
            );
        }

        uksort($grouped, fn (string $a, string $b): int => $grouped[$a]['first_timestamp'] <=> $grouped[$b]['first_timestamp'] ?: $a <=> $b);

        return $grouped;
    }

    protected function buildMigrationMeta(string $path, string $name): array
    {
        $timestamp = $this->extractTimestampPrefix($name);
        if ($timestamp === '') {
            throw new RuntimeException("Invalid migration file name: {$name}");
        }

        return [
            'path' => $path,
            'name' => $name,
            'timestamp' => $timestamp,
            'relative' => str_replace('\\', '/', ltrim(str_replace(base_path(), '', $path), DIRECTORY_SEPARATOR)),
        ];
    }

    protected function extractTimestampPrefix(string $filename): string
    {
        if (preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_/', $filename, $matches)) {
            return $matches[1];
        }

        return '';
    }

    protected function extractMethodBody(string $contents, string $methodName): ?string
    {
        $tokens = token_get_all($contents);
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            if (!is_array($token) || $token[0] !== T_FUNCTION) {
                continue;
            }

            $index = $i + 1;
            while ($index < $count && is_array($tokens[$index]) && $tokens[$index][0] === T_WHITESPACE) {
                $index++;
            }

            if ($index < $count && $tokens[$index] === '&') {
                $index++;
            }

            while ($index < $count && is_array($tokens[$index]) && $tokens[$index][0] === T_WHITESPACE) {
                $index++;
            }

            if ($index >= $count || !is_array($tokens[$index]) || $tokens[$index][0] !== T_STRING || strtolower($tokens[$index][1]) !== strtolower($methodName)) {
                continue;
            }

            while ($index < $count && $tokens[$index] !== '{') {
                $index++;
            }

            if ($index >= $count) {
                return null;
            }

            $depth = 1;
            $index++;
            $body = '';

            while ($index < $count && $depth > 0) {
                $current = $tokens[$index];
                $text = is_array($current) ? $current[1] : $current;

                if ($text === '{') {
                    $depth++;
                } elseif ($text === '}') {
                    $depth--;
                    if ($depth === 0) {
                        $index++;
                        break;
                    }
                }

                if ($depth > 0) {
                    $body .= $text;
                }

                $index++;
            }

            return $body;
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    protected function splitTopLevelStatements(string $body): array
    {
        $statements = [];
        $current = '';
        $braceDepth = 0;
        $parenDepth = 0;
        $bracketDepth = 0;

        $tokens = token_get_all("<?php\n" . $body);

        foreach ($tokens as $token) {
            $text = is_array($token) ? $token[1] : $token;

            if (is_array($token) && $token[0] === T_OPEN_TAG) {
                continue;
            }

            $current .= $text;

            if ($text === '{') {
                $braceDepth++;
            } elseif ($text === '}') {
                $braceDepth = max(0, $braceDepth - 1);
            } elseif ($text === '(') {
                $parenDepth++;
            } elseif ($text === ')') {
                $parenDepth = max(0, $parenDepth - 1);
            } elseif ($text === '[') {
                $bracketDepth++;
            } elseif ($text === ']') {
                $bracketDepth = max(0, $bracketDepth - 1);
            }

            if ($text === ';' && $braceDepth === 0 && $parenDepth === 0 && $bracketDepth === 0) {
                $trimmed = trim($current);
                if ($trimmed !== '') {
                    $statements[] = $trimmed;
                }

                $current = '';
            }
        }

        $remaining = trim($current);
        if ($remaining !== '') {
            $statements[] = $remaining;
        }

        return $statements;
    }

    /**
     * @return array<int, string>
     */
    protected function extractTablesFromStatement(string $statement): array
    {
        $tables = [];

        if (preg_match_all("/Schema::(?:create|table|dropIfExists|drop)\\s*\\(\\s*['\"]([a-zA-Z0-9_]+)['\"]/i", $statement, $matches)) {
            foreach ($matches[1] as $table) {
                $tables[] = strtolower($table);
            }
        }

        if (preg_match_all("/Schema::rename\\s*\\(\\s*['\"]([a-zA-Z0-9_]+)['\"]\\s*,\\s*['\"]([a-zA-Z0-9_]+)['\"]/i", $statement, $renameMatches)) {
            foreach ($renameMatches[1] as $oldName) {
                $tables[] = strtolower($oldName);
            }

            foreach ($renameMatches[2] as $newName) {
                $tables[] = strtolower($newName);
            }
        }

        if (preg_match_all("/DB::table\\s*\\(\\s*['\"]([a-zA-Z0-9_]+)['\"]/i", $statement, $dbTableMatches)) {
            foreach ($dbTableMatches[1] as $table) {
                $tables[] = strtolower($table);
            }
        }

        if (preg_match_all("/(?:ALTER|CREATE|DROP)\\s+TABLE\\s+`?([a-zA-Z0-9_]+)`?/i", $statement, $sqlMatches)) {
            foreach ($sqlMatches[1] as $table) {
                $tables[] = strtolower($table);
            }
        }

        return array_values(array_unique($tables));
    }

    /**
     * @param array<string, array{first_timestamp:string,statements:array<int, array{statement:string,source:string,timestamp:string}>}> $grouped
     */
    protected function printSummary(array $grouped): void
    {
        $rows = [];

        foreach ($grouped as $table => $payload) {
            $rows[] = [
                $table,
                (string) count($payload['statements']),
                $payload['first_timestamp'],
            ];
        }

        $this->table(['Table', 'Merged statements', 'First timestamp'], $rows);
        $this->line('Detected tables: ' . count($rows));
    }

    /**
     * @param array<string, array{first_timestamp:string,statements:array<int, array{statement:string,source:string,timestamp:string}>}> $grouped
     */
    protected function writeConsolidatedFiles(array $grouped, string $outputDirectory): void
    {
        File::ensureDirectoryExists($outputDirectory);

        foreach ($grouped as $table => $payload) {
            $timestamp = $payload['first_timestamp'];
            $filename = "{$timestamp}_consolidated_{$table}_table.php";
            $path = $outputDirectory . DIRECTORY_SEPARATOR . $filename;

            File::put($path, $this->renderMigrationFile($table, $payload['statements']));
            $this->line('Generated: ' . str_replace('\\', '/', ltrim(str_replace(base_path(), '', $path), DIRECTORY_SEPARATOR)));
        }
    }

    protected function renderMigrationFile(string $table, array $statements): string
    {
        $chunks = [];

        foreach ($statements as $entry) {
            $statement = rtrim($entry['statement']);
            $indented = preg_replace('/^/m', '        ', $statement);
            $chunks[] = "        // Source: {$entry['source']}\n{$indented}\n";
        }

        $upBody = rtrim(implode("\n", $chunks));
        if ($upBody === '') {
            $upBody = '        // No operations detected.';
        }

        return <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
{$upBody}
    }

    public function down(): void
    {
        Schema::dropIfExists('{$table}');
    }
};
PHP;
    }

    /**
     * @param array<int, array{path:string,name:string,timestamp:string,relative:string}> $migrationFiles
     */
    protected function archiveOriginalMigrations(array $migrationFiles): void
    {
        $archiveRoot = base_path('database/migrations_archive');
        File::ensureDirectoryExists($archiveRoot);

        foreach ($migrationFiles as $migration) {
            $sourcePath = $migration['path'];

            if (str_contains(str_replace('\\', '/', $sourcePath), '/database/migrations_archive/')) {
                continue;
            }

            $relative = str_replace('\\', '/', $migration['relative']);
            $targetPath = $archiveRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
            $targetDirectory = dirname($targetPath);

            File::ensureDirectoryExists($targetDirectory);
            File::move($sourcePath, $targetPath);

            $this->line('Archived: ' . $relative);
        }
    }
}
