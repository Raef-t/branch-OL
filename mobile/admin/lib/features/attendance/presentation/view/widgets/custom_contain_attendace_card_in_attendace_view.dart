import 'package:flutter/cupertino.dart';
import '/features/attendance/presentation/managers/models/attendance_model.dart';
import '/features/attendance/presentation/view/widgets/custom_left_side_in_attendace_card_in_attendace_view.dart';
import '/features/attendance/presentation/view/widgets/custom_right_side_in_attendace_card_in_attendace_view.dart';

class CustomContainAttendaceCardInAttendaceView extends StatelessWidget {
  const CustomContainAttendaceCardInAttendaceView({
    super.key,
    required this.attendanceModel,
  });
  final AttendanceModel attendanceModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        CustomLeftSideInAttendaceCardInAttendaceView(
          attendanceModel: attendanceModel,
        ),
        const Spacer(),
        CustomRightSideInAttendaceCardInAttendaceView(
          attendanceModel: attendanceModel,
        ),
      ],
    );
  }
}
