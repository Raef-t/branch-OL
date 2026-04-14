import 'package:flutter/cupertino.dart';
import '/core/sized_boxs/widths.dart';
import '/features/attendance/presentation/managers/models/attendance_model.dart';
import '/features/attendance/presentation/view/widgets/custom_three_images_in_attendace_card_in_attendace_view.dart';
import '/features/attendance/presentation/view/widgets/custom_three_texts_in_attendace_card_in_attendace_view.dart';

class CustomThreeImagesAndTextsInAttendaceCardInAttendaceView
    extends StatelessWidget {
  const CustomThreeImagesAndTextsInAttendaceCardInAttendaceView({
    super.key,
    required this.attendanceModel,
  });
  final AttendanceModel attendanceModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        CustomThreeTextsInAttendaceCardInAttendaceView(
          attendanceModel: attendanceModel,
        ),
        Widths.width10(context: context),
        const CustomThreeImagesInAttendaceCardInAttendaceView(),
      ],
    );
  }
}
