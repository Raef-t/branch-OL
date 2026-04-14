import 'package:dio/dio.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';

List<ExamsModel> changeListOfResponseToListOfExamsModelHelper({
  required Response response,
}) {
  final List<dynamic> data = response.data['data'];
  final List<ExamsModel> listOfExamsModel = [];
  for (var exam in data) {
    listOfExamsModel.add(ExamsModel.fromJson(json: exam));
  }
  return listOfExamsModel;
}
