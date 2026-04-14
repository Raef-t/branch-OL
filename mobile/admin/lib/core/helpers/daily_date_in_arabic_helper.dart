import 'package:intl/intl.dart';

String dailyDateInArabicHelper({DateTime? date}) {
  final now = date ?? DateTime.now();
  final formatDate = DateFormat('MMMM d, yyyy', 'ar');
  //this shape to date i need it
  return formatDate.format(now);
}
