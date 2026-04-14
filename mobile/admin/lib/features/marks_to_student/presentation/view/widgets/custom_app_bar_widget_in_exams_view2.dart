import 'package:flutter/material.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_three_texts_component.dart';
import '/features/marks_to_student/presentation/view/widgets/custom_apply_image_in_exams_view2.dart';

class CustomAppBarWidgetInExamsView2 extends StatelessWidget {
  const CustomAppBarWidgetInExamsView2({super.key});

  @override
  Widget build(BuildContext context) {
    return const Row(
      children: [
        CustomApplyImageInExamsView2(),
        Spacer(),
        AppBarWidgetWithRightArrowImageAndThreeTextsComponent(
          firstText: 'العلامات',
          secondText: 'يمكنك الاطلاع على جميع مذاكرات الطالب',
          thirdText: 'في جميع المواد',
        ),
      ],
    );
  }
}
