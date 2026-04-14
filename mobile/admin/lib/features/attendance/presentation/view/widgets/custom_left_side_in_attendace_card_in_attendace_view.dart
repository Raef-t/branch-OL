import 'package:flutter/cupertino.dart';
import '/core/components/svg_image_component.dart';
import '/core/styles/colors_style.dart';
import '/features/attendance/presentation/managers/models/attendance_model.dart';
import '/gen/assets.gen.dart';

class CustomLeftSideInAttendaceCardInAttendaceView extends StatelessWidget {
  const CustomLeftSideInAttendaceCardInAttendaceView({
    super.key,
    required this.attendanceModel,
  });
  final AttendanceModel attendanceModel;
  @override
  Widget build(BuildContext context) {
    return SvgImageComponent(
      pathImage: Assets.images.manyCircleAvatarsImage,
      color: attendanceModel.status == 'present'
          ? ColorsStyle.greenColor2
          : ColorsStyle.redColor,
    );
  }
}
