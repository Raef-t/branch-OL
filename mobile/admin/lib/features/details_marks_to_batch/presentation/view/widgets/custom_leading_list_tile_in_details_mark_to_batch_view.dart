import 'package:flutter/cupertino.dart';
import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';

class CustomLeadingListTileInDetailsMarkToBatchView extends StatelessWidget {
  const CustomLeadingListTileInDetailsMarkToBatchView({
    super.key,
    required this.examsResultToBatchModel,
  });
  final ExamsResultToBatchModel examsResultToBatchModel;
  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return SizedBox(
      height: size.height * (isRotait ? 0.045 : 0.06),
      width: size.width * 0.075,
      child: ClipOval(
        child: Image.network(
          examsResultToBatchModel.studentPhoto != null &&
                  examsResultToBatchModel.studentPhoto!.isNotEmpty
              ? examsResultToBatchModel.studentPhoto!
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
