import 'package:flutter/material.dart';
import '/features/details_marks_to_batch/presentation/managers/models/exams_result_to_batch_model.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_leading_list_tile_in_details_mark_to_batch_view.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_subtitle_list_tile_in_details_mark_to_batch_view.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_title_list_tile_in_details_mark_to_batch_view.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_trailing_list_tile_in_details_mark_to_batch_view.dart';

class CustomListTileInDetailsMarkToBatchView extends StatelessWidget {
  const CustomListTileInDetailsMarkToBatchView({
    super.key,
    required this.examsResultToBatchModel,
  });
  final ExamsResultToBatchModel examsResultToBatchModel;
  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: CustomLeadingListTileInDetailsMarkToBatchView(
        examsResultToBatchModel: examsResultToBatchModel,
      ),
      title: CustomTitleListTileInDetailsMarkToBatchView(
        examsResultToBatchModel: examsResultToBatchModel,
      ),
      subtitle: CustomSubtitleListTileInDetailsMarkToBatchView(
        examsResultToBatchModel: examsResultToBatchModel,
      ),
      trailing: CustomTrailingListTileInDetailsMarkToBatchView(
        examsResultToBatchModel: examsResultToBatchModel,
      ),
    );
  }
}
