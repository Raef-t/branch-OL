import '/features/home/presentation/managers/models/class_schedule/period_model.dart';

class ClassScheduleModel {
  final int? count;
  final List<PeriodModel> listOfPeriodsModel;
  //all period contain on many lessons(حصة1, حصة2,..)
  ClassScheduleModel({required this.count, required this.listOfPeriodsModel});
}
