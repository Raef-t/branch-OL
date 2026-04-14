import 'package:flutter/cupertino.dart';
import '/core/components/text_medium14_component.dart';
import '/core/styles/colors_style.dart';
import '/features/attendance/presentation/managers/models/attendance_model.dart';

class CustomDayTextInAttendaceCardInAttendaceView extends StatelessWidget {
  const CustomDayTextInAttendaceCardInAttendaceView({
    super.key,
    required this.attendanceModel,
  });
  final AttendanceModel attendanceModel;
  @override
  Widget build(BuildContext context) {
    return TextMedium14Component(
      text: attendanceModel.day ?? 'لا يوجد يوم',
      color: ColorsStyle.mediumBlackColor2,
    );
  }
}
