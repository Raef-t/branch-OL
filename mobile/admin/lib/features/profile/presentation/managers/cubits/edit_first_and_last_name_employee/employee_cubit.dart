import 'package:flutter_bloc/flutter_bloc.dart';
import '/features/profile/data/repositories/edit_first_and_last_name_employee/employee_repositories_implementation.dart';
import '/features/profile/presentation/managers/cubits/edit_first_and_last_name_employee/employee_state.dart';

class EmployeeCubit extends Cubit<EmployeeState> {
  EmployeeCubit({required this.employeesRepositoriesImplementation})
    : super(EmployeeInitialState());
  final EmployeesRepositoriesImplementation employeesRepositoriesImplementation;
  Future<void> updateEmployee({
    required int employeeId,
    required String firstName,
    required String lastName,
  }) async {
    emit(EmployeeLoadingState());
    final result = await employeesRepositoriesImplementation.updateEmployee(
      employeeId: employeeId,
      firstName: firstName,
      lastName: lastName,
    );
    result.fold(
      (failure) {
        emit(
          EmployeeFailureState(
            errorMessage: failure.errorMessageInFailureError,
          ),
        );
      },
      (employee) {
        emit(EmployeeSuccessState(employeeModel: employee));
      },
    );
  }
}
