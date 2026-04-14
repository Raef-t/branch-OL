import 'package:dio/dio.dart';
import '/features/teachers/presentation/managers/models/teachers_model.dart';

List<TeachersModel> changeListOfResponseToListOfTeachersModelHelper({
  required Response response,
}) {
  final List data = response.data['data'] as List;
  return data
      .map((e) => TeachersModel.fromJson(json: e as Map<String, dynamic>))
      .toList();
}
