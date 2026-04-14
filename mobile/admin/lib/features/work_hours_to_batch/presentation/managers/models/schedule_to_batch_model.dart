import '/features/work_hours_to_batch/presentation/managers/models/lesson_to_batch_model.dart';
import '/features/work_hours_to_batch/presentation/managers/models/periods_to_batch_model.dart';

class ScheduleToBatchModel {
  final int? periodsCount;
  final List<PeriodsToBatchModel> listOfPeriodsModel;
  ScheduleToBatchModel({
    required this.periodsCount,
    required this.listOfPeriodsModel,
  });
  factory ScheduleToBatchModel.fromJson({required Map<String, dynamic> json}) {
    final Map<String, dynamic> periodsMap = json['periods'];
    if (periodsMap.isEmpty) {
      return ScheduleToBatchModel(
        periodsCount: json['periods_count'],
        listOfPeriodsModel: [],
      );
    } //there is no workHours because the map is empty
    final String dayKey = periodsMap.keys.first;
    //i take first key in the map, and it's dayName(sunday, monday,..)
    final Map<String, dynamic> dayPeriods = periodsMap[dayKey];
    //i give dayName to json to enable me take lessons(give me value this key(day))
    final List<PeriodsToBatchModel>
    listOfPeriodsModel = dayPeriods.entries.map((entry) {
      // تحويل كل درس
      final lessons = (entry.value as List)
          .map((e) => LessonToBatchModel.fromJson(json: e))
          .toList();
      //entries: to deal with map, enable me deal with key and value in this map
      // إزالة التكرار حسب subjectName و batch
      final uniqueLessons = lessons.fold<List<LessonToBatchModel>>([], (
        previous,
        element,
      ) {
        final exists = previous.any(
          (l) =>
              l.subjectName == element.subjectName &&
              l.course == element.course &&
              l.classRoom == element.classRoom &&
              l.startTime == element.startTime,
        );
        if (!exists) previous.add(element);
        return previous;
      });
      return PeriodsToBatchModel(
        periodName: entry.key,
        listOfLessonModel: uniqueLessons,
      );
    }).toList();
    //i filter all lessons to prevent the repeated
    return ScheduleToBatchModel(
      periodsCount: json['periods_count'],
      listOfPeriodsModel: listOfPeriodsModel,
    );
  }
}
