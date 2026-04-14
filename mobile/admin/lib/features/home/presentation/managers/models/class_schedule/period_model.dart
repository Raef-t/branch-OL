import '/features/home/presentation/managers/models/class_schedule/lessons_model.dart';

class PeriodModel {
  final String? periodName;
  final List<LessonsModel> listOfLessonsModel;
  //all lesson(حصة) contain on list of elements
  PeriodModel({required this.periodName, required this.listOfLessonsModel});
}
