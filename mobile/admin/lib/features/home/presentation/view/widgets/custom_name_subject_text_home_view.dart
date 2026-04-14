import 'package:flutter/material.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';

class CustomNameSubjectTextHomeView extends StatelessWidget {
  const CustomNameSubjectTextHomeView({super.key, required this.subjectName});
  final String subjectName;
  @override
  Widget build(BuildContext context) {
    return Text(
      subjectName,
      style: TextsStyle.medium14(
        context: context,
      ).copyWith(color: ColorsStyle.blackColor),
    );
  }
}
