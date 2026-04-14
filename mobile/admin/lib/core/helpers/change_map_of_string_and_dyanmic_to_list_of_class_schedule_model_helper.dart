import 'package:dio/dio.dart';
import '/features/home/presentation/managers/models/class_schedule/class_schedule_model.dart';
import '/features/home/presentation/managers/models/class_schedule/lessons_model.dart';
import '/features/home/presentation/managers/models/class_schedule/period_model.dart';

ClassScheduleModel changeMapOfStringAndDyanmicToListOfClassScheduleModelHelper({
  required Response response,
}) {
  final data = response.data['data'];
  final int count = data['periods_count'];
  final Map<String, dynamic> periodsMap = data['periods'];
  final List<PeriodModel> listOfPeriodModel = [];
  periodsMap.forEach((key, value) {
    //i loop in map, so this key(it's key in map), and this value(it's value in map)
    final List<LessonsModel> listOfLessonsModel = [];
    //i create list to get on all lessons in this حصة
    for (var lesson in value) {
      listOfLessonsModel.add(LessonsModel.fromJson(json: lesson));
    }
    listOfPeriodModel.add(
      PeriodModel(periodName: key, listOfLessonsModel: listOfLessonsModel),
    );
  });
  return ClassScheduleModel(
    count: count,
    listOfPeriodsModel: listOfPeriodModel,
  );
  //i return Model(contain on count and full list to all lessons in all حصص)
}
