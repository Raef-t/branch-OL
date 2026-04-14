import 'package:flutter/material.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_three_texts_component.dart';
import '/features/class/presentation/view/widgets/custom_text_with_checkbox_select_all_in_class_view.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';
import '/gen/assets.gen.dart';

class CustomAppBarWidgetInClassView extends StatelessWidget {
  const CustomAppBarWidgetInClassView({super.key, required this.batchesModel});
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        const CustomTextWithCheckboxSelectAllInClassView(),
        const Spacer(),
        AppBarWidgetWithRightArrowImageAndThreeTextsComponent(
          firstText: batchesModel.batchName?.substring(0, 13) ?? 'لا يوجد شعبه',
          secondText: 'يمكنك الاطلاع على جميع الطلاب',
          thirdText: 'هذه الشعبه',
          image: Assets.images.rightArrowWithLineInCenterItImage.image(),
        ),
      ],
    );
  }
}
