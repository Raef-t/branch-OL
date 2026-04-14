import 'package:flutter/material.dart';
import '/core/components/svg_image_component.dart';
import '/core/styles/colors_style.dart';
import '/gen/assets.gen.dart';

class TrailingListTileDetailsAndAttendanceComponent extends StatelessWidget {
  const TrailingListTileDetailsAndAttendanceComponent({
    super.key,
    required this.studentAttendance,
  });
  final bool studentAttendance;
  @override
  Widget build(BuildContext context) {
    return SvgImageComponent(
      pathImage: Assets.images.manyCircleAvatarsImage,
      color: studentAttendance
          ? ColorsStyle.greenColor2
          : ColorsStyle.mediumRedColor,
    );
  }
}
