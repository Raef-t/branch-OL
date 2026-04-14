import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/work_hours_to_all_students/data/repositories/schedule_to_all_student_repositories_implementation.dart';
import '/features/work_hours_to_all_students/presentation/managers/cubits/schedule_to_all_student_state.dart';

class ScheduleToAllStudentCubit extends Cubit<ScheduleToAllStudentState> {
  final ScheduleToAllStudentRepositoriesImplementation
  scheduleToBatchRepositoriesImplementation;
  ScheduleToAllStudentCubit({
    required this.scheduleToBatchRepositoriesImplementation,
  }) : super(ScheduleToAllStudentInitialState());
  Future<void> getSchedule({String? day}) async {
    emit(ScheduleToAllStudentLoadingState());
    final instituteBranchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyInstituteBranchIdInSharedPreferences,
        );
    final result = await scheduleToBatchRepositoriesImplementation.getSchedule(
      type: 'location',
      id: 1,
      day: day ?? 'today',
      instituteBranchId: (instituteBranchId == null || instituteBranchId == 0)
          ? 1
          : instituteBranchId,
    );
    result.fold(
      (failure) => emit(
        ScheduleToAllStudentFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (scheduleToBatchModel) => emit(
        ScheduleToAllStudentSuccessState(
          scheduleToBatchModelInCubit: scheduleToBatchModel,
        ),
      ),
    );
  }
}
