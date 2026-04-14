String convertStringToUnicodeHexHelper({required String contentMessage}) {
  final buffer = StringBuffer();
  for (int i = 0; i < contentMessage.length; i++) {
    buffer.write(
      contentMessage.codeUnitAt(i).toRadixString(16).padLeft(4, '0'),
    );
  }
  return buffer.toString();
  //here in this helper i convert message(example: مرحبا) to hex message(رسالة مشفرة), MTN just except this message
}
