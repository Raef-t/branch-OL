import 'package:flutter/cupertino.dart';
import '/features/attendance/presentation/managers/models/attendance_model.dart';
import '/features/attendance/presentation/view/widgets/custom_attendace_card_in_attendace_view.dart';

class CustomSuccessStateInAttendaceView extends StatelessWidget {
  const CustomSuccessStateInAttendaceView({
    super.key,
    required this.length,
    required this.listOfAttendaceModel,
  });
  final int length;
  final List<AttendanceModel> listOfAttendaceModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(length, (index) {
        final attendanceModel = listOfAttendaceModel[index];
        return CustomAttendaceCardInAttendaceView(
          attendanceModel: attendanceModel,
        );
      }),
    );
  }
}
