import 'package:flutter/material.dart';
import '/core/styles/texts_style.dart';
import '/gen/fonts.gen.dart';

class CustomNameTeacherInDetailsCardHomeView extends StatelessWidget {
  const CustomNameTeacherInDetailsCardHomeView({super.key, required this.name});
  final String name;
  @override
  Widget build(BuildContext context) {
    return Text(
      name,
      style: TextsStyle.normal10(
        context: context,
      ).copyWith(fontFamily: FontFamily.tajawal),
    );
  }
}
