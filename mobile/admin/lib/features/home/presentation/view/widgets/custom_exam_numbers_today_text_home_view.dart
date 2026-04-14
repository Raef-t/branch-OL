import 'package:flutter/material.dart';
import '/core/components/text_medium13_component.dart';

class CustomExamNumbersTodayTextHomeView extends StatelessWidget {
  const CustomExamNumbersTodayTextHomeView({super.key, required this.length});
  final int length;
  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.centerRight,
      child: TextMedium13Component(text: 'عدد المذاكرات في هذا اليوم $length'),
    );
  }
}
