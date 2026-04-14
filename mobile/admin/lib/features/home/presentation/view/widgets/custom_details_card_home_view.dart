import 'package:flutter/material.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/styles/colors_style.dart';
import '/features/home/presentation/view/widgets/custom_contain_details_card_home_view.dart';
import '/features/home/presentation/managers/models/class_schedule/lessons_model.dart';

class CustomDetailsCardHomeView extends StatefulWidget {
  const CustomDetailsCardHomeView({
    super.key,
    required this.color,
    required this.listOfLessonsModel,
    required this.lengthInListOfLessonsModel,
  });
  final List<LessonsModel> listOfLessonsModel;
  final int lengthInListOfLessonsModel;
  final Color color;
  @override
  State<CustomDetailsCardHomeView> createState() =>
      _CustomDetailsCardHomeViewState();
}

class _CustomDetailsCardHomeViewState extends State<CustomDetailsCardHomeView> {
  int selectedCardInSameTime = 0;
  void goNextCardInSameTime() {
    if (selectedCardInSameTime < (widget.lengthInListOfLessonsModel - 1)) {
      setState(() => selectedCardInSameTime++);
    }
  }

  void goPreviousCardInSameTime() {
    if (selectedCardInSameTime > 0) {
      setState(() => selectedCardInSameTime--);
    }
  }

  @override
  Widget build(BuildContext context) {
    final lessonsModel = widget.listOfLessonsModel[selectedCardInSameTime];
    return Container(
      margin: OnlyPaddingWithoutChild.left20AndBottom23(context: context),
      padding: OnlyPaddingWithoutChild.left12(context: context),
      color: ColorsStyle.mediumWhiteColor,
      child: CustomContainDetailsCardHomeView(
        firstTime: lessonsModel.startTime ?? '00:00 am',
        secondTime: lessonsModel.endTime ?? '00:00 am',
        subjectName: lessonsModel.subjectName ?? 'لا يوجد مادة',
        course: lessonsModel.course ?? 'لا يوجد دورة',
        classRoom: lessonsModel.classRoom ?? 'لا يوجد قاعه',
        supervioserName:
            lessonsModel.supervisorModel?.nameSupervisor ?? 'لا يوجد مشرف',
        imageUrl: lessonsModel.supervisorModel?.photoSupervisor ?? '',
        onLeftArrowTap: goPreviousCardInSameTime,
        onRightArrowTap: goNextCardInSameTime,
        selectedCardInSameTime: selectedCardInSameTime,
        length: widget.lengthInListOfLessonsModel,
        color: widget.color,
      ),
    );
  }
}
