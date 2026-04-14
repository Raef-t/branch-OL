import 'package:flutter_bloc/flutter_bloc.dart';
import '/features/teachers/data/repositories/teachers_repositories_implementation.dart';
import '/features/teachers/presentation/managers/cubits/teachers_state.dart';

class TeachersCubit extends Cubit<TeachersState> {
  TeachersCubit({required this.teachersRepositoriesImplementation})
    : super(TeachersInitialState());
  final TeachersRepositoriesImplementation teachersRepositoriesImplementation;
  Future<void> getAllTeachers() async {
    emit(TeachersLoadingState());
    final result = await teachersRepositoriesImplementation.getAllTeachers();
    result.fold(
      (failure) => emit(
        TeachersFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (teachers) => emit(TeachersSuccessState(teachersListInCubit: teachers)),
    );
  }
}
