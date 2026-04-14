import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/failure_state_component.dart';
import '/features/home/presentation/view/widgets/shimmer_work_hours_home_view.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/features/home/presentation/view/widgets/custom_success_state_from_work_hours_in_home_view.dart';
import '/features/home/presentation/managers/cubits/class_schedule/class_schedule_cubit.dart';
import '/features/home/presentation/managers/cubits/class_schedule/class_schedule_state.dart';

class CustomGenerateDetailsCardHomeView extends StatelessWidget {
  const CustomGenerateDetailsCardHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<ClassScheduleCubit, ClassScheduleState>(
      builder: (context, state) {
        if (state is ClassScheduleSuccessState) {
          final classScheduleModel = state.classScheduleModelInCubit;
          final length = classScheduleModel.count ?? 0;
          if (length == 0) {
            return const TextSuccessStateButTheDataIsEmptyComponent(
              text: 'لا يوجد برنامج دوام',
            );
          } else {
            return CustomSuccessStateFromWorkHoursInHomeView(
              classScheduleModel: classScheduleModel,
              length: length,
            );
          }
        } else if (state is ClassScheduleFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () =>
                context.read<ClassScheduleCubit>().getTodaySchedule(),
          );
        } else {
          return const ShimmerWorkHoursHomeView();
        }
      },
    );
  }
}
