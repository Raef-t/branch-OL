import 'package:flutter/material.dart';
import '/core/components/svg_image_component.dart';
import '/core/styles/colors_style.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';
import '/features/exams_to_all_students/presentation/view/widgets/custom_right_side_in_exam_card_in_exam_view.dart';
import '/gen/assets.gen.dart';

class CustomContainExamCardInExamView extends StatelessWidget {
  const CustomContainExamCardInExamView({
    super.key,
    required this.examsModel,
    required this.subjectColor,
  });
  final ExamsModel examsModel;
  final Color subjectColor;
  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SvgImageComponent(
          pathImage: Assets.images.checkInsideCircleImage,
          color: examsModel.isChecked
              ? ColorsStyle.mediumGreenColor
              : ColorsStyle.mediumRedColor,
        ),
        const Spacer(),
        CustomRightSideInExamCardInExamView(
          examsModel: examsModel,
          subjectColor: subjectColor,
        ),
      ],
    );
  }
}
