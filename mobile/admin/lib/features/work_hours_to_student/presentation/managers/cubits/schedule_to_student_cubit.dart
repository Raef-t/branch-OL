import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/work_hours_to_student/data/repositories/schedule_to_student_repositories_implementation.dart';
import '/features/work_hours_to_student/presentation/managers/cubits/schedule_to_student_state.dart';

class ScheduleToStudentCubit extends Cubit<ScheduleToStudentState> {
  final ScheduleToStudentRepositoriesImplementation
  scheduleToStudentRepositoriesImplementation;
  ScheduleToStudentCubit({
    required this.scheduleToStudentRepositoriesImplementation,
  }) : super(ScheduleToStudentInitialState());
  Future<void> getSchedule({String? day}) async {
    emit(ScheduleToStudentLoadingState());
    final studentId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyStudentIdInSharedPreferences,
    );
    final instituteBranchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyInstituteBranchIdInSharedPreferences,
        );
    final result = await scheduleToStudentRepositoriesImplementation
        .getSchedule(
          type: 'student',
          id: (studentId == null || studentId == 0) ? 1 : studentId,
          day: day ?? 'today',
          instituteBranchId:
              (instituteBranchId == null || instituteBranchId == 0)
              ? 1
              : instituteBranchId,
        );
    result.fold(
      (failure) => emit(
        ScheduleToStudentFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (scheduleToStudentModel) => emit(
        ScheduleToStudentSuccessState(
          scheduleToStudentModelInCubit: scheduleToStudentModel,
        ),
      ),
    );
  }
}
