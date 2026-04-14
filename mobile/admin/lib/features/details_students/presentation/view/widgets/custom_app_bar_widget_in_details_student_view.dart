import 'package:flutter/material.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_three_texts_component.dart';
import '/features/details_students/presentation/view/widgets/custom_notification_card_in_details_student_view.dart';

class CustomAppBarWidgetInDetailsStudentView extends StatelessWidget {
  const CustomAppBarWidgetInDetailsStudentView({super.key});

  @override
  Widget build(BuildContext context) {
    return const Row(
      children: [
        CustomNotificationCardInDetailsStudentView(),
        Spacer(),
        AppBarWidgetWithRightArrowImageAndThreeTextsComponent(
          firstText: 'بيانات الطالب',
          secondText: 'يمكنك الاطلاع على جميع معلومات',
          thirdText: 'الطالب',
        ),
      ],
    );
  }
}
