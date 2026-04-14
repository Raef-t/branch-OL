import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/success_state_for_details_and_attendance_to_student_component.dart';
import '/features/details_students/presentation/managers/cubits/details_students/details_students_cubit.dart';
import '/features/details_students/presentation/managers/cubits/details_students/details_students_state.dart';

class CustomHeaderSectionInAttendaceView extends StatelessWidget {
  const CustomHeaderSectionInAttendaceView({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<DetailsStudentsCubit, DetailsStudentsState>(
      builder: (context, state) {
        if (state is DetailsStudentSuccessState) {
          final detailsStudentsModel = state.detailsStudentsModelInCubit;
          return SuccessStateForDetailsAndAttendanceToStudentComponent(
            detailsStudentsModel: detailsStudentsModel,
          );
        } else if (state is DetailsStudentFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () =>
                context.read<DetailsStudentsCubit>().getDetailsStudentById(),
          );
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
