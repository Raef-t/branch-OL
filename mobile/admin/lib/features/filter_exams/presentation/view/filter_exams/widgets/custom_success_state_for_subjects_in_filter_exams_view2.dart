import 'package:flutter/material.dart';
import '/features/filter_exams/presentation/managers/models/subjects/subjects_model.dart';
import '/features/filter_exams/presentation/view/filter_exams/widgets/custom_subject_card_in_filter_exams_view2.dart';

class CustomSuccessStateForSubjectsInFilterExamsView2 extends StatefulWidget {
  const CustomSuccessStateForSubjectsInFilterExamsView2({
    super.key,
    required this.length,
    required this.listOfSubjectsModel,
  });
  final int length;
  final List<SubjectsModel> listOfSubjectsModel;

  @override
  State<CustomSuccessStateForSubjectsInFilterExamsView2> createState() =>
      _CustomSuccessStateForSubjectsInFilterExamsView2State();
}

class _CustomSuccessStateForSubjectsInFilterExamsView2State
    extends State<CustomSuccessStateForSubjectsInFilterExamsView2> {
  int selectedSubjectCard = 3;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Wrap(
      spacing: size.width * 0.017, //horizontal space
      runSpacing: size.height * (isRotait ? 0.021 : 0.03),
      //vertical space
      children: List.generate(widget.length, (index) {
        final subjectsModel = widget.listOfSubjectsModel[index];
        final subjectName = subjectsModel.subjectName ?? 'لا يوجد مادة';
        return CustomSubjectCardInFilterExamsView2(
          onTap: () => setState(() => selectedSubjectCard = index),
          subjectName: subjectName,
          selectedSubjectCard: selectedSubjectCard,
          index: index,
        );
      }),
    );
  }
}
