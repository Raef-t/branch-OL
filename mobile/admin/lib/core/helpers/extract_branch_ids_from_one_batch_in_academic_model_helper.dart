import '/features/courses_details/presentation/managers/models/academic_branches_courses_details_model.dart';

List<int> extractBranchIdsFromOneBatchInAcademicModelHelper({
  required AcademicBranchesCoursesDetailsModel academicBranchModel,
}) {
  return academicBranchModel.listOfBtachesModel
          ?.map((e) => e.id)
          .whereType<int>()
          .toList() ??
      [];
  //.whereType<type>: this method it's delete all thing in the list type it not same type in this method
  //example: [3, null, 2, 'hi'].whereType<int> so the list will be [3, 2] just, so the .whereType it's remove all elements type them not same type .whereType<type>
}
