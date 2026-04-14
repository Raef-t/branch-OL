import 'package:flutter/material.dart';
import '/core/components/menu_card_component.dart';
import '/features/home/presentation/managers/models/class_schedule/class_schedule_model.dart';

class CustomSuccessStateForCardInWorkHoursView extends StatelessWidget {
  const CustomSuccessStateForCardInWorkHoursView({
    super.key,
    required this.length,
    required this.classScheduleModel,
  });
  final int length;
  final ClassScheduleModel classScheduleModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(length, (index) {
        final listOfLessonsModel =
            classScheduleModel.listOfPeriodsModel[index].listOfLessonsModel;
        final lengthInListOfLessonsModel = classScheduleModel
            .listOfPeriodsModel[index]
            .listOfLessonsModel
            .length;
        return Column(
          children: List.generate(lengthInListOfLessonsModel, (index) {
            final lessonsModel = listOfLessonsModel[index];
            return MenuCardComponent(
              subjectName: lessonsModel.subjectName ?? 'لا يوجد مادة',
              course: lessonsModel.course ?? 'لا يوجد دورة',
              classRoom: lessonsModel.classRoom ?? 'لا يوجد قاعه',
              type: lessonsModel.type ?? 'درس',
              startTime: lessonsModel.startTime ?? '09:00 am',
              endTime: lessonsModel.endTime ?? '10:00 am',
            );
          }),
        );
      }),
    );
  }
}
