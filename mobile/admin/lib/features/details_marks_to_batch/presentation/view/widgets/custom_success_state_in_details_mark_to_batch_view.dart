import 'package:flutter/cupertino.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/features/details_marks_to_batch/presentation/managers/cubits/exams_result_to_batch_state.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_generate_cards_in_details_mark_to_batch_view.dart';

class CustomSuccessStateInDetailsMarkToBatchView extends StatelessWidget {
  const CustomSuccessStateInDetailsMarkToBatchView({
    super.key,
    required this.state,
  });
  final ExamsResultToBatchSuccessState state;
  @override
  Widget build(BuildContext context) {
    final listOfExamsResultToBatchModel =
        state.listOfExamsResultToBatchModelInCubit;
    final length = listOfExamsResultToBatchModel.length;
    if (listOfExamsResultToBatchModel.isEmpty) {
      return const TextSuccessStateButTheDataIsEmptyComponent(
        text: 'لا يوجد طلاب اتموا تقديم هذه المادة',
      );
    }
    return CustomGenerateCardsInDetailsMarkToBatchView(
      length: length,
      listOfExamsResultToBatchModel: listOfExamsResultToBatchModel,
    );
  }
}
