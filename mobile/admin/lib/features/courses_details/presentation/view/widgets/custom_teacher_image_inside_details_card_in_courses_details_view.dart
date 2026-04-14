import 'package:flutter/material.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';

class CustomTeacherImageInsideDetailsCardInCoursesDetailsView
    extends StatelessWidget {
  const CustomTeacherImageInsideDetailsCardInCoursesDetailsView({
    super.key,
    required this.batchesModel,
  });
  final BatchesCoursesDetailsModel batchesModel;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return SizedBox(
      height: size.height * 0.038,
      width: size.width * 0.083,
      child: ClipOval(
        child: Image.network(
          batchesModel.supervisorInAcademicBranchModel?.photoSupervisor !=
                      null &&
                  batchesModel
                      .supervisorInAcademicBranchModel!
                      .photoSupervisor!
                      .isNotEmpty
              ? batchesModel.supervisorInAcademicBranchModel!.photoSupervisor!
              : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpR2mt4DTP5bMkhpMu1eMde4Rg6EFc78CfIg&s',
          fit: BoxFit.fill,
          errorBuilder: (context, error, stackTrace) {
            return Image.network(
              'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpR2mt4DTP5bMkhpMu1eMde4Rg6EFc78CfIg&s',
              fit: BoxFit.fill,
            );
          },
        ),
      ),
    );
  }
}
