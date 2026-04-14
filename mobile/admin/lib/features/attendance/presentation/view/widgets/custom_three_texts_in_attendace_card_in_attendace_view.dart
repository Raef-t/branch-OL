import 'package:flutter/cupertino.dart';
import '/core/components/text_medium12_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/attendance/presentation/managers/models/attendance_model.dart';
import '/gen/fonts.gen.dart';

class CustomThreeTextsInAttendaceCardInAttendaceView extends StatelessWidget {
  const CustomThreeTextsInAttendaceCardInAttendaceView({
    super.key,
    required this.attendanceModel,
  });
  final AttendanceModel attendanceModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        TextMedium12Component(
          text: attendanceModel.date ?? 'لا يوجد تاريخ',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBrownColor,
        ),
        Heights.height10(context: context),
        TextMedium12Component(
          text:
              'وقت الوصول'
              '${attendanceModel.checkIn}',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBrownColor,
        ),
        Heights.height10(context: context),
        TextMedium12Component(
          text:
              'وقت الانصراف '
              '${attendanceModel.checkOut ?? 'لم يتم بعد'}',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBrownColor,
        ),
      ],
    );
  }
}
