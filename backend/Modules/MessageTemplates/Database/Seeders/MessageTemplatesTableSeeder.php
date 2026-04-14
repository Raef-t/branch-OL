<?php

namespace Modules\MessageTemplates\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MessageTemplates\Models\MessageTemplate;

class MessageTemplatesTableSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name'    => 'رسالة تأخر',
                'type'    => 'sms',
                'subject' => null,
                'body'    => 'الطالب {{student_name}} تأخر عن المعهد بتاريخ {{date}} بسبب {{reason}}.',
            ],
            [
                'name'    => 'رسالة غياب',
                'type'    => 'sms',
                'subject' => null,
                'body'    => 'الطالب {{student_name}} غاب عن المعهد بتاريخ {{date}}.',
            ],
            [
                'name'    => 'رسالة غياب بإذن',
                'type'    => 'sms',
                'subject' => null,
                'body'    => 'الطالب {{student_name}} غاب بإذن بتاريخ {{date}} بسبب {{reason}}.',
            ],
            [
                'name'    => 'رسالة تنبيه (داخل التطبيق)',
                'type'    => 'in_app',
                'subject' => 'تنبيه هام',
                'body'    => '{{message}}',
            ],
            [
                'name'    => 'رسالة بريد إلكتروني',
                'type'    => 'email',
                'subject' => 'إشعار من المعهد',
                'body'    => 'عزيزي {{recipient_name}}، {{message}}',
            ],
        ];

        foreach ($templates as $template) {
            MessageTemplate::firstOrCreate(
                [
                    'name' => $template['name'],
                    'type' => $template['type'],
                ],
                [
                    'subject'   => $template['subject'],
                    'body'      => $template['body'],
                    'is_active' => true,
                ]
            );
        }
    }
}
