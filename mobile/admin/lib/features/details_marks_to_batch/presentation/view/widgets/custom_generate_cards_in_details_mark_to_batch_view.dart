import 'package:flutter/cupertino.dart';
import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_card_in_details_mark_to_batch_view.dart';

class CustomGenerateCardsInDetailsMarkToBatchView extends StatelessWidget {
  const CustomGenerateCardsInDetailsMarkToBatchView({
    super.key,
    required this.length,
    required this.listOfExamsResultToBatchModel,
  });
  final int length;
  final List<ExamsResultToBatchModel> listOfExamsResultToBatchModel;
  @override
  Widget build(BuildContext context) {
    return Column(
      children: List.generate(length, (index) {
        final examsResultToBatchModel = listOfExamsResultToBatchModel[index];
        return CustomCardInDetailsMarkToBatchView(
          examsResultToBatchModel: examsResultToBatchModel,
        );
      }),
    );
  }
}
