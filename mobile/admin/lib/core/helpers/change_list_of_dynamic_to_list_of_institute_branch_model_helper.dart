import 'package:dio/dio.dart';
import '/features/home/presentation/managers/models/institute_branch/institute_branch_model.dart';

List<InstituteBranchModel>
changeListOfDynamicToListOfInstituteBranchModelHelper({
  required Response<dynamic> response,
}) {
  final List<dynamic> instituteBranchList = response.data['data'] ?? [];
  final listOfInstituteBranchModel = instituteBranchList
      .map((json) => InstituteBranchModel.fromJson(json: json))
      .toList();
  return listOfInstituteBranchModel;
}
