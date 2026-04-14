import 'package:flutter/material.dart';
import '/core/components/text_medium10_component.dart';
import '/gen/fonts.gen.dart';

class SubtitleListTileDetailsAndAttendanceComponent extends StatelessWidget {
  const SubtitleListTileDetailsAndAttendanceComponent({
    super.key,
    required this.batchName,
  });
  final String batchName;
  @override
  Widget build(BuildContext context) {
    return TextMedium10Component(
      text: batchName,
      fontFamily: FontFamily.tajawal,
    );
  }
}
