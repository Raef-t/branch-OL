import 'package:flutter/cupertino.dart';
import '/core/sized_boxs/heights.dart';
import '/features/attendance/presentation/managers/models/attendance_model.dart';
import '/features/attendance/presentation/view/widgets/custom_day_text_in_attendace_card_in_attendace_view.dart';
import '/features/attendance/presentation/view/widgets/custom_three_images_and_texts_in_attendace_card_in_attendace_view.dart';

class CustomRightSideInAttendaceCardInAttendaceView extends StatelessWidget {
  const CustomRightSideInAttendaceCardInAttendaceView({
    super.key,
    required this.attendanceModel,
  });
  final AttendanceModel attendanceModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        CustomDayTextInAttendaceCardInAttendaceView(
          attendanceModel: attendanceModel,
        ),
        Heights.height9(context: context),
        CustomThreeImagesAndTextsInAttendaceCardInAttendaceView(
          attendanceModel: attendanceModel,
        ),
      ],
    );
  }
}
