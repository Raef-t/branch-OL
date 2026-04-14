import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/features/profile/data/repositories/edit_first_and_last_name_employee/employee_repositories.dart';
import '/features/profile/data/services/edit_first_and_last_name_employee/employee_service.dart';
import '/features/profile/presentation/managers/models/edit_first_and_last_name_employee/employee_model.dart';

class EmployeesRepositoriesImplementation implements EmployeesRepositories {
  final EmployeesService employeesService;
  EmployeesRepositoriesImplementation({required this.employeesService});
  @override
  Future<Either<FailureError, EmployeeModel>> updateEmployee({
    required int employeeId,
    required String firstName,
    required String lastName,
  }) async {
    try {
      final response = await employeesService.updateEmployee(
        employeeId: employeeId,
        firstName: firstName,
        lastName: lastName,
      );
      final employeeModel = EmployeeModel.fromJson(json: response.data['data']);
      return Right(employeeModel);
    } on DioException catch (e) {
      return left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر تعديل اسم و كنية موظف، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
