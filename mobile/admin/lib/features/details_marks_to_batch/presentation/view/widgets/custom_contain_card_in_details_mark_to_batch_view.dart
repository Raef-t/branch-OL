import 'package:flutter/cupertino.dart';
import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_list_tile_in_details_mark_to_batch_view.dart';

class CustomContainCardInDetailsMarkToBatchView extends StatelessWidget {
  const CustomContainCardInDetailsMarkToBatchView({
    super.key,
    required this.examsResultToBatchModel,
  });
  final ExamsResultToBatchModel examsResultToBatchModel;
  @override
  Widget build(BuildContext context) {
    return Directionality(
      textDirection: TextDirection.rtl,
      child: CustomListTileInDetailsMarkToBatchView(
        examsResultToBatchModel: examsResultToBatchModel,
      ),
    );
  }
}
