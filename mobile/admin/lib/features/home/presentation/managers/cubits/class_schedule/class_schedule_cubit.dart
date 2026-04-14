import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/home/data/repositories/class_schedule/class_schedule_repositories_implementation.dart';
import '/features/home/presentation/managers/cubits/class_schedule/class_schedule_state.dart';

class ClassScheduleCubit extends Cubit<ClassScheduleState> {
  final ClassScheduleRepositoryImplementation
  classScheduleRepositoryImplementation;
  ClassScheduleCubit({required this.classScheduleRepositoryImplementation})
    : super(ClassScheduleInitialState());
  Future<void> getTodaySchedule() async {
    emit(ClassScheduleLoadingState());
    final instituteBranchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyInstituteBranchIdInSharedPreferences,
        );
    final result = await classScheduleRepositoryImplementation.getTodaySchedule(
      instituteBranchId: instituteBranchId ?? 1,
    );
    result.fold(
      (failure) => emit(
        ClassScheduleFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (classSchedule) => emit(
        ClassScheduleSuccessState(classScheduleModelInCubit: classSchedule),
      ),
    );
  }
}
