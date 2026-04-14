import 'package:dio/dio.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';

List<AcademicBranchesToCoursesModel>
changeListOfDynamicToListOfAcademicBranchesModelHelper({
  required Response<dynamic> response,
}) {
  final List<dynamic> data = response.data['data'];
  final List<AcademicBranchesToCoursesModel> listOfAcademicBranchesModel = [];
  for (var academicBranches in data) {
    listOfAcademicBranchesModel.add(
      AcademicBranchesToCoursesModel.fromJson(json: academicBranches),
    );
  }
  return listOfAcademicBranchesModel;
}
