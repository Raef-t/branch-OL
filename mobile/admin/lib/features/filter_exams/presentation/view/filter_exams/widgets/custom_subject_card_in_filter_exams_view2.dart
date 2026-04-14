import 'package:flutter/material.dart';
import '/core/decorations/box_decorations.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_contain_subject_card_in_filter_exams_view2.dart';

class CustomSubjectCardInFilterExamsView2 extends StatelessWidget {
  const CustomSubjectCardInFilterExamsView2({
    super.key,
    required this.selectedSubjectCard,
    required this.index,
    required this.subjectName,
    required this.onTap,
  });
  final int selectedSubjectCard, index;
  final String subjectName;
  final void Function() onTap;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: size.width * 0.2,
        height: size.height * (isRotait ? 0.04 : 0.075),
        alignment: Alignment.center,
        decoration: BoxDecorations.boxDecorationToSubjectCardInFilterExamsView2(
          context: context,
          index: index,
          selectedSubjectCard: selectedSubjectCard,
        ),
        child: CustomContainSubjectCardInFilterExamsView2(
          subjectName: subjectName,
          index: index,
          selectedSubjectCard: selectedSubjectCard,
        ),
      ),
    );
  }
}
