import 'package:flutter/cupertino.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/paddings/padding_without_child/symmetric_padding_without_child.dart';
import '/features/attendance/presentation/managers/models/attendance_model.dart';
import '/features/attendance/presentation/view/widgets/custom_contain_attendace_card_in_attendace_view.dart';

class CustomAttendaceCardInAttendaceView extends StatelessWidget {
  const CustomAttendaceCardInAttendaceView({
    super.key,
    required this.attendanceModel,
  });
  final AttendanceModel attendanceModel;
  @override
  Widget build(BuildContext context) {
    return Container(
      margin: OnlyPaddingWithoutChild.right20AndLeft20AndBottom21(
        context: context,
      ),
      padding: SymmetricPaddingWithoutChild.horizontal27AndVertical9(
        context: context,
      ),
      decoration: BoxDecorations.boxDecorationToAttendaceCardInAttendaceView(
        context: context,
      ),
      child: CustomContainAttendaceCardInAttendaceView(
        attendanceModel: attendanceModel,
      ),
    );
  }
}
