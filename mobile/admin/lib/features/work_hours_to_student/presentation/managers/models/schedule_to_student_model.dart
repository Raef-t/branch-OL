import '/features/work_hours_to_student/presentation/managers/models/lesson_to_student_model.dart';
import '/features/work_hours_to_student/presentation/managers/models/periods_to_student_model.dart';

class ScheduleToStudentModel {
  final int? periodsCount;
  final List<PeriodsToStudentModel> listOfPeriodsModel;
  ScheduleToStudentModel({
    required this.periodsCount,
    required this.listOfPeriodsModel,
  });
  factory ScheduleToStudentModel.fromJson({
    required Map<String, dynamic> json,
  }) {
    final Map<String, dynamic> periodsMap = json['periods'];
    if (periodsMap.isEmpty) {
      return ScheduleToStudentModel(
        periodsCount: json['periods_count'],
        listOfPeriodsModel: [],
      );
    } //there is no workHours because the map is empty
    final String dayKey = periodsMap.keys.first;
    //i take first key in the map, and it's dayName(sunday, monday,..)
    final Map<String, dynamic> dayPeriods = periodsMap[dayKey];
    //i give dayName to json to enable me take lessons(give me value this key(day))
    final List<PeriodsToStudentModel>
    listOfPeriodsModel = dayPeriods.entries.map((entry) {
      // تحويل كل درس
      final lessons = (entry.value as List)
          .map((e) => LessonToStudentModel.fromJson(json: e))
          .toList();
      //entries: to deal with map, enable me deal with key and value in this map
      // إزالة التكرار حسب subjectName و batch
      final uniqueLessons = lessons.fold<List<LessonToStudentModel>>([], (
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
      return PeriodsToStudentModel(
        periodName: entry.key,
        listOfLessonModel: uniqueLessons,
      );
    }).toList();
    //i filter all lessons to prevent the repeated
    return ScheduleToStudentModel(
      periodsCount: json['periods_count'],
      listOfPeriodsModel: listOfPeriodsModel,
    );
  }
}
