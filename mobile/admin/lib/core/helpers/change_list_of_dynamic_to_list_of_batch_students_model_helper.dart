import 'package:dio/dio.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';

List<BatchStudentsModel> changeListOfDynamicToListOfBatchStudentsModelHelper({
  required Response response,
}) {
  final List data = response.data['data'];
  return data.map((e) => BatchStudentsModel.fromJson(json: e)).toList();
}
