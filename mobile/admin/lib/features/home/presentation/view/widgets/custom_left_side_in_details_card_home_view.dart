import 'package:flutter/material.dart';
import '/core/components/vertical_line_that_clipper_from_top_left_and_bottom_left_component.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/features/home/presentation/view/widgets/custom_arrow_image_inside_circle_with_click_on_it_in_home_view.dart';
import '/features/home/presentation/view/widgets/custom_details_about_subject_in_details_card_home_view.dart';
import '/gen/assets.gen.dart';

class CustomLeftSideInDetailsCardHomeView extends StatelessWidget {
  const CustomLeftSideInDetailsCardHomeView({
    super.key,
    required this.color,
    required this.onLeftArrowTap,
    required this.onRightArrowTap,
    required this.subjectTime,
    required this.selectedCardInSameTime,
    required this.length,
    required this.course,
    required this.classRoom,
    required this.supervioserName,
    required this.imageUrl,
  });
  final Color color;
  final void Function() onLeftArrowTap, onRightArrowTap;
  final String subjectTime, course, classRoom, supervioserName, imageUrl;
  final int selectedCardInSameTime, length;
  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        CustomArrowImageInsideCircleWithClickOnItInHomeView(
          onTap: onLeftArrowTap,
          pathImage: Assets.images.leftArrowInsideCircleImage,
          color: selectedCardInSameTime == 0
              ? ColorsStyle.veryLittleRussetColor
              : ColorsStyle.russetColor,
        ),
        Widths.width3(context: context),
        CustomArrowImageInsideCircleWithClickOnItInHomeView(
          onTap: onRightArrowTap,
          pathImage: Assets.images.rightArrowInsideCircleImage,
          color: selectedCardInSameTime == (length - 1)
              ? ColorsStyle.veryLittleRussetColor
              : ColorsStyle.russetColor,
        ),
        CustomDetailsAboutSubjectInDetailsCardHomeViewSection(
          subjectName: subjectTime,
          course: course,
          classRoom: classRoom,
          supervioserName: supervioserName,
          imageUrl: imageUrl,
        ),
        Widths.width9(context: context),
        VerticalLineThatClipperFromTopLeftAndBottomLeftComponent(color: color),
      ],
    );
  }
}
