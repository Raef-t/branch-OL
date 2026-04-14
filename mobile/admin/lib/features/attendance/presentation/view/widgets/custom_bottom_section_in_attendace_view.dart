import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/features/attendance/presentation/managers/cubits/attendance_cubit.dart';
import '/features/attendance/presentation/managers/cubits/attendance_state.dart';
import '/features/attendance/presentation/view/widgets/custom_success_state_in_attendace_view.dart';

class CustomBottomSectionInAttendaceView extends StatelessWidget {
  const CustomBottomSectionInAttendaceView({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<AttendanceCubit, AttendanceState>(
      builder: (context, state) {
        if (state is AttendanceSuccessState) {
          final listOfAttendaceModel = state.listOfAttendaceModelInCubit;
          final length = listOfAttendaceModel.length;
          return CustomSuccessStateInAttendaceView(
            length: length,
            listOfAttendaceModel: listOfAttendaceModel,
          );
        } else if (state is AttendanceFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () => context.read<AttendanceCubit>().getAttendanceLog(),
          );
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
