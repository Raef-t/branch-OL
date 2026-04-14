import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/sized_boxs/heights.dart';
import '/features/details_marks_to_batch/presentation/managers/cubits/exams_result_to_batch_cubit.dart';
import '/features/details_marks_to_batch/presentation/managers/cubits/exams_result_to_batch_state.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_header_section_in_details_mark_to_batch_view.dart';
import '/features/details_marks_to_batch/presentation/view/widgets/custom_success_state_in_details_mark_to_batch_view.dart';

class CustomSliverFillRemainingInDetailsMarkToBatchView
    extends StatelessWidget {
  const CustomSliverFillRemainingInDetailsMarkToBatchView({super.key});

  @override
  Widget build(BuildContext context) {
    return SliverFillRemaining(
      hasScrollBody: false,
      child: BackgroundBodyToViewsComponent(
        child: Column(
          children: [
            Heights.height34(context: context),
            const CustomHeaderSectionInDetailsMarkToBatchView(),
            Heights.height31(context: context),
            BlocBuilder<ExamsResultToBatchCubit, ExamsResultToBatchState>(
              builder: (context, state) {
                if (state is ExamsResultToBatchSuccessState) {
                  return CustomSuccessStateInDetailsMarkToBatchView(
                    state: state,
                  );
                } else if (state is ExamsResultToBatchFailureState) {
                  return FailureStateComponent(
                    errorText: state.errorMessageInCubit,
                    onPressed: () => context
                        .read<ExamsResultToBatchCubit>()
                        .getExamsResults(),
                  );
                } else {
                  return const CircleLoadingStateComponent();
                }
              },
            ),
          ],
        ),
      ),
    );
  }
}
