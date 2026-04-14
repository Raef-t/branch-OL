import 'package:flutter/material.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';

class CustomStudentImageInClassView extends StatelessWidget {
  const CustomStudentImageInClassView({
    super.key,
    required this.batchStudentsModel,
  });
  final BatchStudentsModel batchStudentsModel;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return SizedBox(
      height: size.height * (isRotait ? 0.045 : 0.06),
      width: size.width * 0.075,
      child: ClipOval(
        child: Image.network(
          batchStudentsModel.profilePhoto != null &&
                  batchStudentsModel.profilePhoto!.isNotEmpty
              ? batchStudentsModel.profilePhoto!
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
