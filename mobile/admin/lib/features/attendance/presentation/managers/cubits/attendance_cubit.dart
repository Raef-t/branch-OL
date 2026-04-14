import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/attendance/data/repositories/attendance_repositories_implementation.dart';
import 'attendance_state.dart';

class AttendanceCubit extends Cubit<AttendanceState> {
  AttendanceCubit({required this.attendanceRepositoryImplementation})
    : super(AttendanceInitialState());
  final AttendanceRepositoriesImplementation attendanceRepositoryImplementation;
  Future<void> getAttendanceLog() async {
    emit(AttendanceLoadingState());
    final studentId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyStudentIdInSharedPreferences,
    );
    final result = await attendanceRepositoryImplementation.getAttendanceLog(
      studentId: studentId ?? 1,
      range: 'week',
    );
    result.fold(
      (failure) => emit(
        AttendanceFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (attendanceList) => emit(
        AttendanceSuccessState(listOfAttendaceModelInCubit: attendanceList),
      ),
    );
  }
}
