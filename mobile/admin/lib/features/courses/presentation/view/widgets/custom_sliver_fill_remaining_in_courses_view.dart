import 'package:flutter/material.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/courses/presentation/view/widgets/custom_generate_card_with_circle_in_courses_view.dart';
import '/features/courses/presentation/view/widgets/custom_header_section_in_courses_view.dart';
import '/features/courses/presentation/view/widgets/custom_put_big_circle_in_many_of_dots_image_in_courses_view.dart';
import '/features/courses/presentation/view/widgets/custom_two_texts_in_courses_view.dart';

class CustomSliverFillRemainingInCoursesView extends StatelessWidget {
  const CustomSliverFillRemainingInCoursesView({super.key});

  @override
  Widget build(BuildContext context) {
    return SliverToBoxAdapter(
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            Heights.height48(context: context),
            const CustomHeaderSectionInCoursesView(),
            Heights.height39(context: context),
            const CustomPutBigCircleInManyOfDotsImageInCoursesView(),
            Heights.height14(context: context),
            const CustomTwoTextsInCoursesView(),
            Heights.height39(context: context),
            const CustomGenerateCardWithCircleInCoursesView(),
            Heights.height10(context: context),
          ],
        ),
      ),
    );
  }
}
