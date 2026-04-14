import 'package:flutter/material.dart';
import '/core/lists/colors_to_work_hour_cards_in_home_view_list.dart';
import '/features/home/presentation/view/widgets/custom_details_card_home_view.dart';
import '/features/home/presentation/managers/models/class_schedule/class_schedule_model.dart';

class CustomSuccessStateFromWorkHoursInHomeView extends StatelessWidget {
  const CustomSuccessStateFromWorkHoursInHomeView({
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
        final color =
            colorsToWorkHourCardsInHomeViewList[index %
                colorsToWorkHourCardsInHomeViewList.length];
        return CustomDetailsCardHomeView(
          listOfLessonsModel: listOfLessonsModel,
          color: color,
          lengthInListOfLessonsModel: lengthInListOfLessonsModel,
        );
      }),
    );
  }
}
