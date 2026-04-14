import 'package:flutter/material.dart';
import '/core/components/text_medium16_component.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';
import '/features/courses_details/presentation/view/widgets/custom_date_texts_inside_details_card_in_courses_details_view.dart';
import '/gen/fonts.gen.dart';

class CustomThreeTextsInsideDetailsCardInCardInCoursesDetailsView
    extends StatelessWidget {
  const CustomThreeTextsInsideDetailsCardInCardInCoursesDetailsView({
    super.key,
    required this.batchesModel,
  });
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        CustomDateTextsInsideDetailsCardInCoursesDetailsView(
          date: batchesModel.startDate ?? 'لا يوجد تاريخ',
        ),
        Heights.height10(context: context),
        TextMedium16Component(
          text: batchesModel.isClassroomFull == 1 ? 'مكتملة' : 'غير مكتملة',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.greyColor,
        ),
        Heights.height10(context: context),
        TextMedium16Component(
          text:
              batchesModel.supervisorInAcademicBranchModel?.nameSupervisor ??
              'لا يوجد اسم',
          fontFamily: FontFamily.tajawal,
          color: ColorsStyle.mediumBlackColor2,
        ),
      ],
    );
  }
}
