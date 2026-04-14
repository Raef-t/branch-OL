import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/details_students/data/repositories/details_students/details_students_repositories_implementation.dart';
import '/features/details_students/presentation/managers/cubits/details_students/details_students_state.dart';

class DetailsStudentsCubit extends Cubit<DetailsStudentsState> {
  DetailsStudentsCubit({
    required this.detailsStudentsRepositoriesImplementation,
  }) : super(DetailsStudentInitialState());
  final DetailsStudentsRepositoriesImplementation
  detailsStudentsRepositoriesImplementation;
  Future<void> getDetailsStudentById() async {
    emit(DetailsStudentLoadingState());
    final studentId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyStudentIdInSharedPreferences,
    );
    final result = await detailsStudentsRepositoriesImplementation
        .getDetailsStudentById(studentId: studentId ?? 0);
    result.fold(
      (failure) => emit(
        DetailsStudentFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (detailsStudent) => emit(
        DetailsStudentSuccessState(detailsStudentsModelInCubit: detailsStudent),
      ),
    );
  }
}
