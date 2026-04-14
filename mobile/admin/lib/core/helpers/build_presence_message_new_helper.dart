String buildPresenceMessage({
  required List<String> studentNames,
  required String status,
}) {
  return studentNames.map((name) => 'الطالب $name $status').join('\n');
}
